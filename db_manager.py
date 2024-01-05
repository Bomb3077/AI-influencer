if '__main__' == __name__:
    import pinecone, config

    pinecone.init(api_key=config.PINECONE_API_KEY, environment="gcp-starter")
    # pinecone.create_index("first-index", dimension=3)
    print(pinecone.describe_index("first-index"))


    
